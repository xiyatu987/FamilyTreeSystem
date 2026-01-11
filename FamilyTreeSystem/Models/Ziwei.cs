using System.ComponentModel;
using System.ComponentModel.DataAnnotations;
using System.ComponentModel.DataAnnotations.Schema;

namespace FamilyTreeSystem.Models
{
    /// <summary>
    /// 字辈模型
    /// </summary>
        public class Ziwei
    {
        /// <summary>
        /// 字辈主键ID
        /// </summary>
        [Key]
        public int ZiweiId { get; set; }

        /// <summary>
        /// 字辈字符，必填，最长10个字符
        /// </summary>
        [Required]
        [StringLength(2)]
        [DisplayName("字辈字符")]
        public required string ZiweiChar { get; set; }

        /// <summary>
        /// 世代，用于标识该字辈对应的家族传承代数（默认等于序号，自增）
        /// </summary>
        [DisplayName("世代")]
        [Required]
        public int Generation { get; set; }
        
        /// <summary>
        /// 字辈描述，用于说明该字辈的含义或来源
        /// </summary>
        [DisplayName("描述")]
        public string? Description { get; set; }

        /// <summary>
        /// 创建该字辈的用户ID，必填
        /// </summary>
        [Required]
        public int UserId { get; set; }

        /// <summary>
        /// 字辈创建时间，默认当前时间
        /// </summary>
        public DateTime CreatedAt { get; set; } = DateTime.Now;
        
        /// <summary>
        /// 字辈更新时间，默认当前时间
        /// </summary>
        public DateTime UpdatedAt { get; set; } = DateTime.Now;

        /// <summary>
        /// 创建该字辈的用户导航属性
        /// </summary>
        [ForeignKey("UserId")]
        public User? User { get; set; }

        /// <summary>
        /// 使用该字辈的家族成员列表导航属性集合
        /// </summary>
        public ICollection<FamilyMember> FamilyMembers { get; set; } = new List<FamilyMember>();
    }
}